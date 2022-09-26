export interface ILan {
  session: string;
  date: Date;
}

export class Lan implements ILan {
  constructor(public session: string, public date: Date) {}
}
