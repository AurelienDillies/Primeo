import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { jwtDecode } from 'jwt-decode';
import { User } from '../models/user.model';
import { Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { finalize, shareReplay, tap } from 'rxjs/operators';

type JwtPayload = {
  roles?: string[];
};

export type UserProfileUpdate = Pick<User, 'first_name' | 'last_name' | 'email'> & {
  password?: string;
};

export type RegisterPayload = Pick<User, 'first_name' | 'last_name' | 'email' | 'password'> & {
  role?: string;
  roles?: string[];
};

@Injectable({
  providedIn: 'root',
})
export class UserService {
  user: User | null = null;

  private apiUrl = 'http://localhost:8080/api';
  private tokenKey = 'jwt_token';

  private readonly userCache = new Map<string, User>();
  private readonly inFlightUserRequests = new Map<string, Observable<User>>();

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  login(email: string, password: string) {
    return this.http.post<{ token: string }>(
      `${this.apiUrl}/login`,
      { email, password }
    );
  }

  register(user: RegisterPayload) {
    const role = user.role ?? user.roles?.[0] ?? 'student';

    return this.http.post(`${this.apiUrl}/register`, {
      first_name: user.first_name,
      last_name: user.last_name,
      email: user.email,
      password: user.password,
      role,
    });
  }

  update(user: UserProfileUpdate) {
    return this.http.put<User>(`${this.apiUrl}/users/me`, user).pipe(
      tap((updatedUser) => {
        this.storeUserInCache('me', updatedUser);
      })
    );
  }

  getUsers() {
    return this.http.get<User[]>(`${this.apiUrl}/users/`);
  }

  updateById(id: number, user: UserProfileUpdate) {
    return this.http.put<User>(`${this.apiUrl}/users/${id}`, user).pipe(
      tap((updatedUser) => {
        this.storeUserInCache(`id:${id}`, updatedUser);
      })
    );
  }

  deleteById(id: number) {
    return this.http.delete<void>(`${this.apiUrl}/users/${id}`).pipe(
      tap(() => {
        this.userCache.delete(`id:${id}`);
        this.inFlightUserRequests.delete(`id:${id}`);
      })
    );
  }

  logout(): void {
    this.clearToken();
    this.user = null;
    this.userCache.clear();
    this.inFlightUserRequests.clear();
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

  getUserInfo(id?: number) {
    const cacheKey = typeof id === 'number' ? `id:${id}` : 'me';
    const cachedUser = this.userCache.get(cacheKey);

    if (cachedUser) {
      return of(cachedUser);
    }

    const inFlightRequest = this.inFlightUserRequests.get(cacheKey);
    if (inFlightRequest) {
      return inFlightRequest;
    }

    const endpoint = typeof id === 'number'
      ? `${this.apiUrl}/users/${id}`
      : `${this.apiUrl}/users/me`;

    const request$ = this.http
      .get<User>(endpoint)
      .pipe(
        tap((user) => {
          this.storeUserInCache(cacheKey, user);
        }),
        finalize(() => {
          this.inFlightUserRequests.delete(cacheKey);
        }),
        shareReplay({ bufferSize: 1, refCount: false })
      );

    this.inFlightUserRequests.set(cacheKey, request$);

    return request$;
  }

  clearUserCache(): void {
    this.userCache.clear();
    this.inFlightUserRequests.clear();
  }

  private storeUserInCache(cacheKey: string, user: User): void {
    this.userCache.set(cacheKey, user);

    if (typeof user.id === 'number') {
      this.userCache.set(`id:${user.id}`, user);
    }
  }
}