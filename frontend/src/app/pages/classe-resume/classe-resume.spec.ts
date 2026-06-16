import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ClasseResume } from './classe-resume';

describe('ClasseResume', () => {
  let component: ClasseResume;
  let fixture: ComponentFixture<ClasseResume>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ClasseResume],
    }).compileComponents();

    fixture = TestBed.createComponent(ClasseResume);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
