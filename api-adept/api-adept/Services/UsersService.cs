using api_adept.Context;
using api_adept.Models;
using api_adept.Models.Errors;
using Microsoft.EntityFrameworkCore;
using System.Net;

// For more information on enabling Web API for empty projects, visit https://go.microsoft.com/fwlink/?LinkID=397860

namespace api_adept.Services
{

    public class UsersService : AdeptService, IUsersService
    {
        private DbSet<User> users { get; }
        public UsersService(AdeptLanContext adeptContext) : base(adeptContext)
        {
            users = adeptContext.Users;

        }

        public IEnumerable<User> Get()
        {
            return users.ToList();
        }


        public User GetById(Guid id)
        {
            return users.Find(id);
        }

        public User GetByFirebaseId(string firebaseId)
        {
            return users.FirstOrDefault(x => x.FirebaseId == firebaseId);
        }

        public User SignUp(User value)
        {
            User user = this.users.FirstOrDefault(x => x.Id == value.Id || x.FirebaseId == value.FirebaseId);
            if (user != null)
            {
                throw new AlreadyExistsException("USER", "That user already exists");
            }
            else
            {
                User output = this.users.Add(value).Entity;
                this.SaveChanges();
                return output;
            }
        }

        public void Update(Guid id, string value)
        {
            throw new NotImplementedException();
        }

        public void Delete(Guid id)
        {
            throw new NotImplementedException();
        }

        private void AddOrUpdate(User user)
        {
            User dbUser = this.users.FirstOrDefault(x => x.Id == user.Id || x.FirebaseId == user.FirebaseId);
            if (string.IsNullOrWhiteSpace(user.FirebaseId))
            {
                throw new AdeptException("USER__MISSING_FIREBASEID", "FirebaseID is required on a user", HttpStatusCode.BadRequest);
            }
            if (dbUser == null)
            {
                this.users.Add(user);
            }
            else
            {
                this.users.Update(user);
            }

        }
    }
}
