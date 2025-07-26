import {AbstractControl, ValidationErrors, ValidatorFn} from "@angular/forms";

export function passwordMatchValidator(): ValidatorFn {
  return (control: AbstractControl): ValidationErrors | null => {
    const password = control.get('password')?.value;
    const password2 = control.get('password2')?.value;

    return password && password2 && password !== password2
      ? {passwordMismatch: true}
      : null;
  };
}
