namespace api_adept.Models
{
    public class Lan : BaseModel
    {
        public DateTime Date { get; set; }
        public virtual ISet<Seat> Seats { get; set; } = new HashSet<Seat>();
        public string Session { get; set; }

        protected Lan() { /* Needed for EntityFramework */ }

        public Lan(DateTime date, String session)
        {
            this.Date = date;
            this.Session = session;
        }
    }
}
