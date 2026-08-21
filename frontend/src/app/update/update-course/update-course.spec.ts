import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateCourse } from './update-course';
import { provideHttpClient } from '@angular/common/http';
import { provideRouter } from '@angular/router';

describe('UpdateCourse', () => {
  let component: UpdateCourse;
  let fixture: ComponentFixture<UpdateCourse>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateCourse],
      providers: [provideHttpClient(), provideRouter([])],
    }).compileComponents();

    fixture = TestBed.createComponent(UpdateCourse);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
