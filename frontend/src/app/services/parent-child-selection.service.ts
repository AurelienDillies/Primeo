import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class ParentChildSelectionService {
  private readonly storageKey = 'parent_selected_child_id';

  getSelectedChildId(): number | null {
    const value = localStorage.getItem(this.storageKey);
    if (!value) {
      return null;
    }

    const parsed = Number.parseInt(value, 10);
    return Number.isNaN(parsed) ? null : parsed;
  }

  setSelectedChildId(childId: number): void {
    localStorage.setItem(this.storageKey, String(childId));
  }

  clearSelectedChildId(): void {
    localStorage.removeItem(this.storageKey);
  }
}
