import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AcademicActivity, AcademicClass, AcademicCourse } from '../models/academic.model';
import { Observable } from 'rxjs';
import { shareReplay } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class AdminAcademicDataService {
  private readonly apiUrl = 'http://localhost:8080/api/admin';
  private allClasses$?: Observable<AcademicClass[]>;
  private allCourses$?: Observable<AcademicCourse[]>;
  private allActivities$?: Observable<AcademicActivity[]>;

  constructor(private readonly http: HttpClient) {}

  getAllClasses() {
    if (!this.allClasses$) {
      this.allClasses$ = this.http
        .get<AcademicClass[]>(`${this.apiUrl}/classes`)
        .pipe(shareReplay({ bufferSize: 1, refCount: false }));
    }

    return this.allClasses$;
  }

  getAllCourses() {
    if (!this.allCourses$) {
      this.allCourses$ = this.http
        .get<AcademicCourse[]>(`${this.apiUrl}/courses`)
        .pipe(shareReplay({ bufferSize: 1, refCount: false }));
    }

    return this.allCourses$;
  }

  getAllActivities() {
    if (!this.allActivities$) {
      this.allActivities$ = this.http
        .get<AcademicActivity[]>(`${this.apiUrl}/activities`)
        .pipe(shareReplay({ bufferSize: 1, refCount: false }));
    }

    return this.allActivities$;
  }

  clearCache(): void {
    this.allClasses$ = undefined;
    this.allCourses$ = undefined;
    this.allActivities$ = undefined;
  }
}