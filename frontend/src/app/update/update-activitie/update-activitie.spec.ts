import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateActivitie } from './update-activitie';
import { provideHttpClient } from '@angular/common/http';
import { provideRouter } from '@angular/router';

describe('UpdateActivitie', () => {
  let component: UpdateActivitie;
  let fixture: ComponentFixture<UpdateActivitie>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateActivitie],
      providers: [provideHttpClient(), provideRouter([])],
    }).compileComponents();

    fixture = TestBed.createComponent(UpdateActivitie);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
