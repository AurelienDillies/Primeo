import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Activitie } from './activitie';

describe('Activitie', () => {
  let component: Activitie;
  let fixture: ComponentFixture<Activitie>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Activitie],
    }).compileComponents();

    fixture = TestBed.createComponent(Activitie);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
