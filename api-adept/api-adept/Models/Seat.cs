using System.ComponentModel.DataAnnotations;

namespace api_adept.Models
{
    public class Seat : BaseModel 
    {
        public int Number { get; set; }

        [RegularExpression(@"/^[A-Z]+$/i")]
        public char Section { get; set; }
        
        public string Place => $"{Section}{Number}";

        public virtual Lan Lan { get; set; }
        public virtual Reservation Reservation { get; set; }

        protected Seat() { /* Needed for EntityFramework */ }

        public Seat(Lan lan)
        {
            Lan = lan;
        }
    }
}
