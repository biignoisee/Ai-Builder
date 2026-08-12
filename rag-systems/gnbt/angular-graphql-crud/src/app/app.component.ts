import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Apollo, gql } from 'apollo-angular';

// --- Definición de GraphQL ---
const GET_USERS = gql`
  query {
    users {
      id
      name
      email
    }
  }
`;

const CREATE_USER = gql`
  mutation CreateUser($name: String!, $email: String!) {
    createUser(name: $name, email: $email) {
      id
      name
      email
    }
  }
`;

const DELETE_USER = gql`
  mutation DeleteUser($id: Int!) {
    deleteUser(id: $id)
  }
`;

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, FormsModule], // Importante importar FormsModule para el ngModel
  template: `
    <div class="container">
      <h1>🚀 CRUD Usuarios GraphQL</h1>

      <!-- Formulario Crear -->
      <div class="card">
        <h3>Agregar Usuario</h3>
        <input type="text" [(ngModel)]="newName" placeholder="Nombre" />
        <input type="email" [(ngModel)]="newEmail" placeholder="Email" />
        <button (click)="addUser()" [disabled]="!newName || !newEmail">Guardar</button>
      </div>

      <!-- Tabla Leer / Eliminar -->
      <div class="card">
        <h3>Lista de Usuarios</h3>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let user of users">
              <td>{{ user.id }}</td>
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>
                <button class="btn-danger" (click)="deleteUser(user.id)">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  `,
  styles: [`
    .container { font-family: sans-serif; max-width: 800px; margin: 2rem auto; }
    .card { background: #f9f9f9; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
    input { padding: 0.5rem; margin-right: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
    button { padding: 0.5rem 1rem; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
    button:disabled { background: #ccc; }
    .btn-danger { background: #dc3545; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
  `]
})
export class AppComponent implements OnInit {
  users: any[] = [];
  newName = '';
  newEmail = '';

  constructor(private apollo: Apollo) { }

  ngOnInit() {
    this.loadUsers();
  }

  // QUERY: Leer usuarios
  loadUsers() {
    this.apollo.watchQuery<any>({
      query: GET_USERS,
      fetchPolicy: 'network-only' // Para siempre traer la info fresca
    }).valueChanges.subscribe(({ data }) => {
      this.users = data.users;
    });
  }

  // MUTATION: Crear
  addUser() {
    this.apollo.mutate({
      mutation: CREATE_USER,
      variables: {
        name: this.newName,
        email: this.newEmail
      },
      refetchQueries: [{ query: GET_USERS }] // Refresca la tabla automáticamente
    }).subscribe(() => {
      this.newName = '';
      this.newEmail = '';
    });
  }

  // MUTATION: Eliminar
  deleteUser(id: number) {
    this.apollo.mutate({
      mutation: DELETE_USER,
      variables: { id: id },
      refetchQueries: [{ query: GET_USERS }]
    }).subscribe();
  }
}
