import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateClasse } from './update-classe';

describe('UpdateClasse', () => {
  let component: UpdateClasse;
  let fixture: ComponentFixture<UpdateClasse>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateClasse],
    }).compileComponents();

    fixture = TestBed.createComponent(UpdateClasse);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
