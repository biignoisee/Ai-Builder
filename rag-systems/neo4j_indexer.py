import os
import re
from neo4j import GraphDatabase

# Configuración de conexión a Neo4j
URI = "bolt://localhost:7687"
AUTH = ("neo4j", "password123")

def determine_node_type(filepath):
    """Clasifica el archivo según su ubicación en la estructura del proyecto."""
    if "routes" in filepath:
        return "RouteFile"
    elif filepath.endswith(".blade.php"):
        if "/flux/" in filepath or "flux" in filepath.lower():
            return "FluxComponent"
        elif "/components/" in filepath:
            return "BladeComponent"
        elif "/layouts/" in filepath:
            return "Layout"
        elif "/Livewire/" in filepath: # Vistas de Livewire
            return "LivewireView"
        return "View"
    elif filepath.endswith(".php"):
        if "/Controllers/" in filepath:
            return "Controller"
        elif "/Models/" in filepath:
            return "Model"
        elif "/Livewire/" in filepath:
            return "LivewireComponent"
        elif "/Actions/" in filepath:
            return "Action"
        elif "/Enums/" in filepath:
            return "Enum"
        elif "/Providers/" in filepath:
            return "Provider"
        return "Class"
    return "Unknown"

def extract_php_metadata(content):
    """Extrae clases, imports (uses) y llamadas a vistas de un archivo PHP."""
    class_match = re.search(r'class\s+(\w+)', content)
    class_name = class_match.group(1) if class_match else None

    # Extraer dependencias (use App\Models\User;)
    imports = re.findall(r'use\s+([^;]+);', content)

    # Extraer vistas llamadas, ej: view('pages.dashboard') o return view('livewire.user')
    views_called = re.findall(r"view\(\s*['\"]([^'\"]+)['\"]\s*\)", content)

    return class_name, imports, views_called

def extract_blade_metadata(content):
    """Extrae inclusiones de componentes dentro de un archivo Blade."""
    # Extrae componentes como <x-app-layout>, <flux:button>, <livewire:table>
    components = re.findall(r"<(?:x-|livewire:|flux:)([\w\-\.]+)", content)
    # Extrae directivas @livewire('nombre')
    livewire_directives = re.findall(r"@livewire\(\s*['\"]([^'\"]+)['\"]\s*\)", content)

    return components + livewire_directives

def extract_routes(content):
    """Extrae las definiciones de rutas y los controladores que invocan."""
    # Busca patrones como Route::get('/ruta', [Controller::class, 'metodo'])
    routes = re.findall(r"Route::\w+\s*\(\s*['\"]([^'\"]+)['\"]\s*,\s*\[([^:]+)::class", content)
    return routes

def process_file(driver, filepath, base_dir):
    """Procesa un archivo individual y lo inserta en la base de datos de grafos."""
    node_type = determine_node_type(filepath)
    if node_type == "Unknown":
        return

    with open(filepath, 'r', encoding='utf-8') as file:
        try:
            content = file.read()
        except UnicodeDecodeError:
            return

    # Usar rutas relativas para el almacenamiento
    relative_path = os.path.relpath(filepath, base_dir)

    with driver.session() as session:
        if node_type == "RouteFile":
            # Procesar el archivo de rutas (web.php)
            routes = extract_routes(content)
            for route_path, controller in routes:
                session.run(
                    """
                    MERGE (r:Route {name: $route_path, filepath: $filepath})
                    MERGE (c:Controller {name: $controller})
                    MERGE (r)-[:CALLS]->(c)
                    """,
                    route_path=route_path, filepath=relative_path, controller=controller
                )
            print(f"Indexado [Rutas]: {relative_path} con {len(routes)} endpoints detectados.")

        elif filepath.endswith(".blade.php"):
            # Procesar archivos de interfaz gráfica
            # Normalizar el nombre de la vista basado en su ruta (ej: pages/dashboard.blade.php -> pages.dashboard)
            view_name = relative_path.replace('resources/views/', '').replace('/', '.').replace('.blade.php', '')
            components = extract_blade_metadata(content)

            session.run(
                """
                MERGE (v:View {name: $view_name})
                SET v.filepath = $filepath, v.type = $node_type
                """,
                view_name=view_name, filepath=relative_path, node_type=node_type
            )

            for comp in set(components):
                session.run(
                    """
                    MATCH (v:View {name: $view_name})
                    MERGE (c:Component {name: $comp_name})
                    MERGE (v)-[:INCLUDES]->(c)
                    """,
                    view_name=view_name, comp_name=comp
                )
            print(f"Indexado [{node_type}]: {view_name}")

        elif filepath.endswith(".php"):
            # Procesar Clases PHP (Controladores, Modelos, Livewire)
            class_name, imports, views_called = extract_php_metadata(content)

            if class_name:
                session.run(
                    """
                    MERGE (n:Class {name: $name})
                    SET n.filepath = $filepath, n.type = $node_type
                    """,
                    name=class_name, filepath=relative_path, node_type=node_type
                )

                # Relacionar Imports
                for imp in imports:
                    imported_class = imp.split('\\')[-1]
                    if imported_class:
                        session.run(
                            """
                            MATCH (n:Class {name: $name})
                            MERGE (dep:Class {name: $dep_name})
                            MERGE (n)-[:USES]->(dep)
                            """,
                            name=class_name, dep_name=imported_class
                        )

                # Relacionar Vistas
                for view in views_called:
                    session.run(
                        """
                        MATCH (n:Class {name: $name})
                        MERGE (v:View {name: $view_name})
                        MERGE (n)-[:RENDERS]->(v)
                        """,
                        name=class_name, view_name=view
                    )
                print(f"Indexado [{node_type}]: {class_name}")

def main():
    # Defina la ruta raíz del proyecto
    base_dir = "/home/biignoisee/Documents/biignoisee/Ai-Builder/rag-systems/citaSync/citaSync"

    # Carpetas a escanear
    target_dirs = ["app", "routes", "resources/views"]

    print("Iniciando indexación estructural en Neo4j...")
    driver = GraphDatabase.driver(URI, auth=AUTH)

    for target in target_dirs:
        full_path = os.path.join(base_dir, target)
        for root, _, files in os.walk(full_path):
            for file in files:
                if file.endswith((".php", ".blade.php")):
                    filepath = os.path.join(root, file)
                    process_file(driver, filepath, base_dir)

    driver.close()
    print("Indexación completada correctamente.")

if __name__ == "__main__":
    main()
