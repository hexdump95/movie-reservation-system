import {Component} from '@angular/core';
import {MatFormField, MatFormFieldModule} from "@angular/material/form-field";
import {MatInput, MatInputModule} from "@angular/material/input";
import {FormBuilder, FormsModule, ReactiveFormsModule, Validators} from "@angular/forms";
import {MatButton, MatButtonModule, MatIconButton} from "@angular/material/button";
import {MatIcon, MatIconModule} from "@angular/material/icon";
import {Router, RouterLink} from "@angular/router";
import {MatDialogModule} from "@angular/material/dialog";
import {environment} from "../../../environments/environment";
import {AuthService} from "../auth.service";
import {LoginRequest} from "../interfaces/login-request";

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    MatFormField,
    MatInput,
    FormsModule,
    MatButton,
    MatIcon,
    MatIconButton,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    ReactiveFormsModule,
    MatDialogModule,
    RouterLink,
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {
  pageTitle = environment.pageTitle;
  hide = true;

  formLogin = this.fb.group({
    username: ['', [Validators.required, Validators.email]],
    password: ['', Validators.required],
  });

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router,
  ) {
  }

  loginSubmitted = false;

  onSubmit() {
    if (!this.formLogin.valid)
      return;

    this.loginSubmitted = !this.loginSubmitted;
    const user: LoginRequest = {
      username: this.formLogin.value.username!,
      password: this.formLogin.value.password!,
    };

    this.authService.login(user).subscribe(
      {
        next: res => {
          let authHeader = res.access_token;
          if (authHeader.length > 0) {
            this.authService.setToken(authHeader);
          }
          void this.router.navigate(["/"]);
        },
        error: _ => {
          this.loginSubmitted = !this.loginSubmitted;
        }
      }
    );
  }

}
