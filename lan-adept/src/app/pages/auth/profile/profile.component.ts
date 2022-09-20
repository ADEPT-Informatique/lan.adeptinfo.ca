import { Component, OnInit } from "@angular/core";
import { first, firstValueFrom } from "rxjs";
import { User } from "src/app/core/models/user";
import { UserService } from "src/app/core/services/user.service";

@Component({
  templateUrl: "./profile.component.html",
  styleUrls: ["./profile.component.scss"],
})
export class ProfileComponent implements OnInit {
  public user!: User;
  constructor(private _userService: UserService) {}

  async ngOnInit(): Promise<void> {
    this.user = await firstValueFrom(this._userService.me());
  }
}
