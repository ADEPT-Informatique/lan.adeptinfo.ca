using api_adept.Models;
using Microsoft.EntityFrameworkCore;

namespace api_adept.Services
{
    public interface IUsersService
    {
        IEnumerable<User> Get();
        User GetById(Guid id);
        User GetByFirebaseId(string firebaseId);
        User SignUp(User value);
        void Update(Guid id, string value);
        void Delete(Guid id);
    }
}