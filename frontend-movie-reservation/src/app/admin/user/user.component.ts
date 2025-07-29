import {Component} from '@angular/core';
import {UserService} from "../../core/user.service";
import {LoaderComponent} from "../../shared/loader/loader.component";

@Component({
  selector: 'app-user',
  standalone: true,
  imports: [
    LoaderComponent
  ],
  templateUrl: './user.component.html',
  styleUrl: './user.component.css'
})
export class UserComponent {
  usersAndRoles: any;
  checkboxesDisabled = false;

  constructor(private userService: UserService) {
  }

  ngOnInit() {
    this.userService.getUsersAndRoles().subscribe(
      (res: any) => {
        this.usersAndRoles = res;
      }
    );
  }

  addDeleteRole(userId: number, role: string) {
    this.checkboxesDisabled = true;
    this.userService.updateRoleUser(userId, role).subscribe(
      (res: any) => {
        this.checkboxesDisabled = false;
      }
    );
  }
}
