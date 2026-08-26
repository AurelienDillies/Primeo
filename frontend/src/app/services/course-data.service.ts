import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { shareReplay } from 'rxjs/operators';
import { AcademicActivity, AcademicCourse } from '../models/academic.model';

export type CoursePayload = {
  courseTitle: string;
  courseDescription: string;
  classId: number;
  courseResourcefile?: string | null;
  courseVideoUrl?: string | null;
};

export type ActivityPayload = {
  activityType: string;
  activityTitle: string;
  activityDescription: string | null;
  activityDate: string;
  courseId: number;
};

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

  getCourseById(courseId: number): Observable<AcademicCourse & { classId?: number }> {
    return this.http.get<AcademicCourse & { classId?: number }>(`${this.apiUrl}/${courseId}`);
  }

  createCourse(payload: CoursePayload) {
    return this.http.post<{ id: number }>(this.apiUrl, payload);
  }

  updateCourse(courseId: number, payload: Partial<CoursePayload>) {
    return this.http.put<void>(`${this.apiUrl}/${courseId}`, payload);
  }

  deleteCourse(courseId: number) {
    return this.http.delete<void>(`${this.apiUrl}/${courseId}`);
  }

  getActivityById(activityId: number): Observable<AcademicActivity & { courseId?: number }> {
    return this.http.get<AcademicActivity & { courseId?: number }>(`http://localhost:8080/api/activities/${activityId}`);
  }

  createActivity(payload: ActivityPayload) {
    return this.http.post<{ id: number }>('http://localhost:8080/api/activities', payload);
  }

  updateActivity(activityId: number, payload: Partial<ActivityPayload>) {
    return this.http.put<void>(`http://localhost:8080/api/activities/${activityId}`, payload);
  }

  deleteActivity(activityId: number) {
    return this.http.delete<void>(`http://localhost:8080/api/activities/${activityId}`);
  }

  clearCache(): void {
    this.activitiesByCourseIdCache.clear();
  }
}
