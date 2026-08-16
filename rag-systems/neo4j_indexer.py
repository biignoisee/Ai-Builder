import os
import re
from neo4j import GraphDatabase

# 1. Configuración de conexión a tu contenedor Docker de Neo4j
URI = "bolt://localhost:7687"
AUTH = ("neo4j", "password123") # Tus credenciales del docker-compose

def extract_metadata(filepath):
    """Extrae el nombre de la clase y las dependencias (imports) de un archivo PHP."""
    with open(filepath, 'r', encoding='utf-8') as file:
        content = file.read()

    # Regex básico para encontrar el nombre de la clase
    class_match = re.search(r'class\s+(\w+)', content)
    class_name = class_match.group(1) if class_match else None

    # Regex para encontrar los 'use' (imports)
    imports = re.findall(r'use\s+([^;]+);', content)

    # Determinar el tipo de componente basado en la ruta o nombre
    node_type = "Class"
    if "Controller" in filepath:
        node_type = "Controller"
    elif "Models" in filepath:
        node_type = "Model"

    return class_name, node_type, imports

def index_to_neo4j(driver, filepath, class_name, node_type, imports):
    """Inserta el nodo y sus relaciones en Neo4j usando Cypher."""
    with driver.session() as session:
        # Crear el nodo principal
        session.run(
            f"""
            MERGE (n:{node_type} {{name: $name}})
            SET n.filepath = $filepath
            """,
            name=class_name, filepath=filepath
        )

        # Crear relaciones basadas en los imports
        for imp in imports:
            imported_class = imp.split('\\')[-1] # Obtener solo el nombre final (ej. Patient)
            if imported_class:
                # Asumimos que si importa algo, depende de ello (USES)
                session.run(
                    f"""
                    MATCH (n:{node_type} {{name: $name}})
                    MERGE (dep:Dependency {{name: $dep_name}}) // Nodo genérico temporal
                    MERGE (n)-[:USES]->(dep)
                    """,
                    name=class_name, dep_name=imported_class
                )

def main():
    # Ruta a tu proyecto Laravel (ajusta las carpetas según necesites)
    base_dir = "/home/biignoisee/Documents/biignoisee/Ai-Builder/rag-systems/citaSync/citaSync/app"

    driver = GraphDatabase.driver(URI, auth=AUTH)

    print("Iniciando indexación en Neo4j...")

    for root, _, files in os.walk(base_dir):
        for file in files:
            if file.endswith(".php"):
                filepath = os.path.join(root, file)
                class_name, node_type, imports = extract_metadata(filepath)

                if class_name:
                    print(f"Indexando [{node_type}]: {class_name}...")
                    index_to_neo4j(driver, filepath, class_name, node_type, imports)

    driver.close()
    print("¡Indexación 10/10 completada")

if __name__ == "__main__":
    main()
