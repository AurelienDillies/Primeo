import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ParentChildDetail, ParentChildSummary } from '../models/academic.model';
import { Observable } from 'rxjs';
import { shareReplay } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class ParentDataService {
  private readonly apiUrl = 'http://localhost:8080/api/parents/me/children';
  private myChildren$?: Observable<ParentChildSummary[]>;
  private readonly childDetailsCache = new Map<number, Observable<ParentChildDetail>>();

  constructor(private readonly http: HttpClient) {}

  getMyChildren() {
    if (!this.myChildren$) {
      this.myChildren$ = this.http
        .get<ParentChildSummary[]>(this.apiUrl)
        .pipe(shareReplay({ bufferSize: 1, refCount: false }));
    }

    return this.myChildren$;
  }

  getChildDetail(childId: number) {
    const cachedChildDetail = this.childDetailsCache.get(childId);
    if (cachedChildDetail) {
      return cachedChildDetail;
    }

    const childDetail$ = this.http
      .get<ParentChildDetail>(`${this.apiUrl}/${childId}`)
      .pipe(shareReplay({ bufferSize: 1, refCount: false }));

    this.childDetailsCache.set(childId, childDetail$);

    return childDetail$;
  }

  clearCache(): void {
    this.myChildren$ = undefined;
    this.childDetailsCache.clear();
  }
}
