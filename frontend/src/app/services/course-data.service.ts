import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { shareReplay } from 'rxjs/operators';
import { AcademicActivity } from '../models/academic.model';

@Injectable({
  providedIn: 'root',
})
export class CourseDataService {
  private readonly apiUrl = 'http://localhost:8080/api/courses';
  private readonly activitiesByCourseIdCache = new Map<number, Observable<AcademicActivity[]>>();

  constructor(private readonly http: HttpClient) {}

  getActivitiesByCourseId(courseId: number): Observable<AcademicActivity[]> {
    const cached = this.activitiesByCourseIdCache.get(courseId);
    if (cached) {
      return cached;
    }

    const request$ = this.http
      .get<AcademicActivity[]>(`${this.apiUrl}/${courseId}/activities`)
      .pipe(shareReplay({ bufferSize: 1, refCount: false }));

    this.activitiesByCourseIdCache.set(courseId, request$);

    return request$;
  }

  clearCache(): void {
    this.activitiesByCourseIdCache.clear();
  }
}
