using System.ComponentModel.DataAnnotations;

namespace api_adept.Models
{
    public class Seat : BaseModel 
    {
        public virtual Lan Lan { get; set; }

        protected Seat() { /* Needed for EntityFramework */ }

        public Seat(Lan lan)
        {
            Lan = lan;
        }
    }
}
