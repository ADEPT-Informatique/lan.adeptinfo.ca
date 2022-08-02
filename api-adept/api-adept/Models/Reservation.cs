namespace api_adept.Models
{
    #nullable disable
    public class Reservation: BaseModel
    {
        public DateTime Date { get; set; } = DateTime.Now;

        public virtual Lan Lan { get; set; }
        public virtual Seat Seat { get; set; }
    }
}
