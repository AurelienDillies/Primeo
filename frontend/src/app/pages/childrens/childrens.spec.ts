import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Childrens } from './childrens';

describe('Childrens', () => {
  let component: Childrens;
  let fixture: ComponentFixture<Childrens>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Childrens],
    }).compileComponents();

    fixture = TestBed.createComponent(Childrens);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
