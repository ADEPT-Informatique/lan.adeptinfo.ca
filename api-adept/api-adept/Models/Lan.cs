﻿namespace api_adept.Models
{
    public class Lan : BaseModel
    {
        public DateTime Date { get; set; }
        public virtual ISet<Seat> Seats { get; set; }
        public virtual ISet<Reservation> Reservations { get; set; }
        public string Session { get; set; }

        protected Lan() { /* Needed for EntityFramework */ }

        public Lan(string session)
        {
            Seats = new HashSet<Seat>();
            Reservations = new HashSet<Reservation>();
            Session = session;
        }
    }
}
