export class User {
  uid!: string;
  email!: string;
  displayName!: string;
  photoURL!: string;
  emailVerified!: boolean;
  seats?: string[];
}
