import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { jwtDecode } from 'jwt-decode';
import { User } from '../models/user.model';
import { Router } from '@angular/router';

type JwtPayload = {
  roles?: string[];
};

@Injectable({
  providedIn: 'root',
})
export class UserService {
  user: User | null = null;
  private apiUrl = 'http://localhost:8080/api';
  private tokenKey = 'jwt_token';

  constructor(private http: HttpClient, private router: Router) { }

  login(email: string, password: string) {
    return this.http.post<{ token: string }>(`${this.apiUrl}/login`, { email, password });
  }

  register(user: Pick<User, 'first_name' | 'last_name' | 'email' | 'password' | 'roles'>) {
    return this.http.post(`${this.apiUrl}/register`, user);
  }

  update(user: Pick<User, 'first_name' | 'last_name' | 'email' | 'password'>) {
  return this.http.put<User>(`${this.apiUrl}/profile`, user);
}

  logout(): void {
    this.clearToken();
    this.user = null;
    this.router.navigate(['/connexion']);
  }

  setToken(token: string): void {
    localStorage.setItem(this.tokenKey, token);
  }

  clearToken(): void {
    localStorage.removeItem(this.tokenKey);
  }

  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  getUserRoles(): string[] {
    const token = this.getToken();
    if (!token) {
      return [];
    }

    try {
      const decoded = jwtDecode<JwtPayload>(token);
      return decoded.roles ?? [];
    } catch {
      return [];
    }
  }

  hasAnyRole(roles: string[]): boolean {
    const userRoles = this.getUserRoles();
    return roles.some((role) => userRoles.includes(role));
  }
  
  getUserId(): number | null {
    const token = this.getToken();
    if (!token) {
      return null;
    }

    try {
      const decoded = jwtDecode<{ id: number }>(token);
      return decoded.id;
    } catch {
      return null;
    }
  }

  getUserInfo(id : number) {
    return this.http.get<User>(`${this.apiUrl}/users/${id}`); // Remplacez {id} par l'ID de l'utilisateur connecté
  }
}
