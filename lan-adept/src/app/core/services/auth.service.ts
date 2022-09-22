import { Injectable } from "@angular/core";
import * as auth from "firebase/auth";
import { AngularFireAuth } from "@angular/fire/compat/auth";
import { Router } from "@angular/router";
import { filter } from "rxjs";
import firebase from "firebase/compat/app";
import { User } from "firebase/auth";
@Injectable({
  providedIn: "root",
})
export class AuthService {
  public userData!: User; // Save logged in user data

  public get currentUser(): User {
    return this.userData;
  }

  constructor(
    public afAuth: AngularFireAuth, // Inject Firebase auth service
    public router: Router,
  ) {
    /* Saving user data in localstorage when
    logged in and setting up null when logged out */
    this.afAuth.authState.pipe(filter((user) => !!user)).subscribe((user) => {
      if (user !== null) {
        this.userData = user as User;
        localStorage.setItem("user", JSON.stringify(this.userData));
        JSON.parse(localStorage.getItem("user")!);
      } else {
        localStorage.setItem("user", "null");
        JSON.parse(localStorage.getItem("user")!);
      }
    });
  }

  // Returns true when user is looged in and email is verified
  public get isLoggedIn(): boolean {
    const user = JSON.parse(localStorage.getItem("user")!);
    return user !== null;
  }

  public logout(): void {
    this.afAuth.signOut().then(() => {
      localStorage.removeItem("user");
      this.router.navigate([""]);
    });
  }

  // Sign in with email/password
  public signIn(email: string, password: string) {
    return this.afAuth
      .signInWithEmailAndPassword(email, password)
      .then((result) => {
        this.setUser(result);
      })
      .catch((error) => {
        window.alert(error.message);
      });
  }

  public setUser(result: firebase.auth.UserCredential) {
    if (result?.user) {
      this.userData = result.user as User;
      this.router.navigate(["Home"]);
    }
  }

  // Sign up with email/password
  public signUp(email: string, password: string) {
    return this.afAuth
      .createUserWithEmailAndPassword(email, password)
      .then((result) => {
        this.setUser(result);
      })
      .catch((error) => {
        window.alert(error.message);
      });
  }

  // Auth logic to run auth providers
  public authLogin(provider: any) {
    return this.afAuth
      .signInWithPopup(provider)
      .then((result) => {
        this.setUser(result);
      })
      .catch((error) => {
        window.alert(error);
      });
  }

  // Sign in with Google
  public googleAuth() {
    return this.authLogin(new auth.GoogleAuthProvider());
  }

  public forgotPassword(passwordResetEmail: string) {
    return this.afAuth
      .sendPasswordResetEmail(passwordResetEmail)
      .then(() => {
        window.alert("Password reset email sent, check your inbox.");
      })
      .catch((error) => {
        window.alert(error);
      });
  }
}
