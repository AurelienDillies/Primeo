import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateCourse } from './update-course';

describe('UpdateCourse', () => {
  let component: UpdateCourse;
  let fixture: ComponentFixture<UpdateCourse>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateCourse],
    }).compileComponents();

    fixture = TestBed.createComponent(UpdateCourse);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
