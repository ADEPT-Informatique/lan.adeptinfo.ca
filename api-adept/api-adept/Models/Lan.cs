namespace api_adept.Models
{
    public class Lan: BaseModel
    {
        public DateTime Date { get; set; }
        public virtual ICollection<Seat> Seats { get; set; }
        public virtual ICollection<Reservation> Reservations { get; set; }

        public Lan()
        {
            Seats = new HashSet<Seat>();
            Reservations = new HashSet<Reservation>();
        }
    }
}
