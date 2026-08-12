import { Injectable } from '@angular/core';
import { Apollo, gql } from 'apollo-angular';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

// Definimos el Query usando GraphQL
const GET_USERS = gql`
  query GetUsers {
    users {
      id
      name
      email
    }
  }
`;

@Injectable({
  providedIn: 'root'
})
export class UserService {
  constructor(private apollo: Apollo) { }

  getUsers(): Observable<any[]> {
    return this.apollo.watchQuery<any>({
      query: GET_USERS
    }).valueChanges.pipe(
      map(result => result.data.users)
    );
  }
}
