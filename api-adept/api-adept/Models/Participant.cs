using Microsoft.EntityFrameworkCore;

namespace api_adept.Models
{
    [Index("Email", IsUnique = true)]
    public class Participant : BaseModel
    {
        public string FirstName { get; set; }

        public string LastName { get; set; }

        public string Email { get; set; }
    }
}
