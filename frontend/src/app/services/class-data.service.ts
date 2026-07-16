import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { shareReplay } from 'rxjs/operators';
import { AcademicCourse } from '../models/academic.model';

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

  clearCache(): void {
    this.coursesByClassIdCache.clear();
  }
}
