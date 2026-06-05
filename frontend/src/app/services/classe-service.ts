import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Classe } from '../models/classe.model';

@Injectable({
  providedIn: 'root',
})
export class ClasseService {
  private apiUrl = 'http://localhost:8080/api/classes';
  private http: HttpClient = inject(HttpClient);

  getClasses(): Observable<Classe[]> {
    return this.http.get<Classe[]>(this.apiUrl);
  }
}
