import { Lan } from "src/app/core/models/lan";

export function buildLanFromRawResponse(response: any): Lan {
  const date = response.date;

  return new Lan(response.session, new Date(date));
}
