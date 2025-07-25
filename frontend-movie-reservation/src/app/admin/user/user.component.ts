import {Component} from '@angular/core';
import {AdminService} from "../admin.service";
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

  constructor(private adminService: AdminService) {
  }

  ngOnInit() {
    this.adminService.getUsersAndRoles().subscribe(
      (res: any) => {
        this.usersAndRoles = res;
      }
    );
  }

  addDeleteRole(userId: number, role: string) {
    this.checkboxesDisabled = true;
    this.adminService.updateRoleUser(userId, role).subscribe(
      (res: any) => {
        this.checkboxesDisabled = false;
      }
    );
  }
}
