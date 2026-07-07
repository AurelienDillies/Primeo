import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreateActivitie } from './create-activitie';

describe('CreateActivitie', () => {
  let component: CreateActivitie;
  let fixture: ComponentFixture<CreateActivitie>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateActivitie],
    }).compileComponents();

    fixture = TestBed.createComponent(CreateActivitie);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
