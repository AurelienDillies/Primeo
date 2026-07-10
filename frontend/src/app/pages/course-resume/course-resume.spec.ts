import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CourseResume } from './course-resume';

describe('CourseResume', () => {
  let component: CourseResume;
  let fixture: ComponentFixture<CourseResume>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CourseResume],
    }).compileComponents();

    fixture = TestBed.createComponent(CourseResume);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
