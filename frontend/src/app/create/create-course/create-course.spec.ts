import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreateCourse } from './create-course';
import { provideHttpClient } from '@angular/common/http';
import { provideRouter } from '@angular/router';

describe('CreateCourse', () => {
  let component: CreateCourse;
  let fixture: ComponentFixture<CreateCourse>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateCourse],
      providers: [provideHttpClient(), provideRouter([])],
    }).compileComponents();

    fixture = TestBed.createComponent(CreateCourse);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
