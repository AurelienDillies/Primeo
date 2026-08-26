import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { ParentChildSummary } from '../../models/academic.model';
import { ParentDataService } from '../../services/parent-data.service';
import { ParentChildSelectionService } from '../../services/parent-child-selection.service';
import { Observable, of } from 'rxjs';
import { catchError, map, tap } from 'rxjs/operators';

type ChildrenVm = {
  loading: boolean;
  error: string | null;
  children: ParentChildSummary[];
  selectedChildId: number | null;
};

@Component({
  selector: 'app-children',
  imports: [CommonModule],
  templateUrl: './children.html',
  styleUrl: './children.css',
})
export class Children {
  vm$: Observable<ChildrenVm>;
  selectedChildId: number | null;

  private readonly parentDataService = inject(ParentDataService);
  private readonly parentChildSelectionService = inject(ParentChildSelectionService);
  private readonly router = inject(Router);

  constructor() {
    this.selectedChildId = this.parentChildSelectionService.getSelectedChildId();
    this.vm$ = this.parentDataService.getMyChildren().pipe(
      tap((children) => {
        if (this.selectedChildId && !children.some((child) => child.id === this.selectedChildId)) {
          this.parentChildSelectionService.clearSelectedChildId();
          this.selectedChildId = null;
        }

        // Auto-select first child to avoid requiring an initial click.
        if (!this.selectedChildId && children.length > 0) {
          this.selectChild(children[0].id);
        }
      }),
      map((children) => ({
        loading: false,
        error: null,
        children,
        selectedChildId: this.selectedChildId,
      })),
      catchError(() =>
        of({
          loading: false,
          error: 'Impossible de charger la liste des enfants pour le moment.',
          children: [],
          selectedChildId: this.selectedChildId,
        })
      )
    );
  }

  selectChild(childId: number): void {
    this.selectedChildId = childId;
    this.parentChildSelectionService.setSelectedChildId(childId);
  }

  openProgress(childId: number): void {
    this.selectChild(childId);
    this.router.navigate(['/suivi']);
  }
}
