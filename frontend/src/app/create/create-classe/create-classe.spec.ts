import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreateClasse } from './create-classe';

describe('CreateClasse', () => {
  let component: CreateClasse;
  let fixture: ComponentFixture<CreateClasse>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateClasse],
    }).compileComponents();

    fixture = TestBed.createComponent(CreateClasse);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
