using api_adept.Core;
using Microsoft.EntityFrameworkCore;

namespace api_adept.Services
{
    public interface IUsersService
    {
        DbSet<User> users { get; }

        void Delete(int id);
        IEnumerable<User> Get();
        User Get(Guid id);
        User Get(string firebaseId);
        void Post(string value);
        void Put(int id, string value);
    }
}