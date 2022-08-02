using System.ComponentModel.DataAnnotations;

namespace api_adept.Models
{
    #nullable disable
    public class Seat: BaseModel
    {
        public int Number { get; set; }

        [RegularExpression("/^[A-Z]+$/")]
        public char Section { get; set; }
        
        public string Place => $"{Section}{Number}";

        public virtual Lan Lan { get; set; }
    }
}
