using api_adept.Models;

namespace api_adept.Core
{
    public class User
    {
        public virtual Guid Id { get; set; }
        public virtual string FirebaseId { get; set; }
        public virtual string Email { get; set; }
        public virtual IEnumerable<Seat> seats { get; set; }

        public User()
        {

        }
    }
}
