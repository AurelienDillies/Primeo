import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormsProfile } from './forms-profile';

describe('FormsProfile', () => {
  let component: FormsProfile;
  let fixture: ComponentFixture<FormsProfile>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormsProfile],
    }).compileComponents();

    fixture = TestBed.createComponent(FormsProfile);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
