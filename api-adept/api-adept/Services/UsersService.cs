using api_adept.Context;
using api_adept.Core;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

// For more information on enabling Web API for empty projects, visit https://go.microsoft.com/fwlink/?LinkID=397860

namespace api_adept.Services
{

    public class UsersService : IUsersService
    {
        public DbSet<User> users { get; }
        public UsersService(AdeptLanContext adeptContext)
        {
            users = adeptContext.Users;
        }

        public IEnumerable<User> Get()
        {
            return users.ToList();
        }


        public User Get(Guid id)
        {
            return users.Find(id);
        }


        public void Post(string value)
        {
        }


        public void Put(int id, string value)
        {
        }


        public void Delete(int id)
        {
        }
    }
}
