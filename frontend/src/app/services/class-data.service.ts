import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { shareReplay } from 'rxjs/operators';
import { AcademicClass, AcademicCourse, AcademicStudent, AcademicTeacher } from '../models/academic.model';

@Injectable({
  providedIn: 'root',
})
export class ClassDataService {
  private readonly apiUrl = 'http://localhost:8080/api/classes';
  private readonly coursesByClassIdCache = new Map<number, Observable<AcademicCourse[]>>();

  constructor(private readonly http: HttpClient) {}

  getCoursesByClassId(classId: number): Observable<AcademicCourse[]> {
    const cached = this.coursesByClassIdCache.get(classId);
    if (cached) {
      return cached;
    }

    const request$ = this.http
      .get<AcademicCourse[]>(`${this.apiUrl}/${classId}/courses`)
      .pipe(shareReplay({ bufferSize: 1, refCount: false }));

    this.coursesByClassIdCache.set(classId, request$);

    return request$;
  }

  getClasses(): Observable<AcademicClass[]> {
    return this.http.get<AcademicClass[]>(`${this.apiUrl}/`);
  }

  getClassById(classId: number): Observable<AcademicClass> {
    return this.http.get<AcademicClass>(`${this.apiUrl}/${classId}`);
  }

  getStudents(): Observable<AcademicStudent[]> {
    return this.http.get<AcademicStudent[]>('http://localhost:8080/api/users/students');
  }

  getTeachers(): Observable<AcademicTeacher[]> {
    return this.http.get<AcademicTeacher[]>('http://localhost:8080/api/users/teachers');
  }

  createClass(payload: Pick<AcademicClass, 'className' | 'classDescription'> & { studentIds: number[]; teacherId: number | null }) {
    return this.http.post<{ id: number }>(`${this.apiUrl}/`, payload);
  }

  updateClass(classId: number, payload: Partial<Pick<AcademicClass, 'className' | 'classDescription'>> & { studentIds?: number[]; teacherId?: number | null }) {
    return this.http.put<void>(`${this.apiUrl}/${classId}`, payload);
  }

  deleteClass(classId: number) {
    return this.http.delete<void>(`${this.apiUrl}/${classId}`);
  }

  clearCache(): void {
    this.coursesByClassIdCache.clear();
  }
}
