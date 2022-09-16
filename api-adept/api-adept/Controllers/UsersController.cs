using api_adept.Models;
using api_adept.Services;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using System.Net.Http.Headers;
using System.Security.Claims;
using System.Web.Http.Filters;

// For more information on enabling Web API for empty projects, visit https://go.microsoft.com/fwlink/?LinkID=397860

namespace api_adept.Controllers
{
    [Authorize]
    [ApiController]
    [Route("api/users")]
    public class UsersController : AdeptController
    {
        public UsersController(IUsersService userService) : base(userService)
        {
        }

        // GET: api/users/me
        [HttpGet("me")]
        public User Me()
        {
            return User;
        }

        // GET api/<UsersController>/5
        [HttpGet("{id}")]
        public Guid Get(Guid id)
        {
            return id;
        }

        // POST api/<UsersController>
        [HttpPost]
        public void SignUp([FromBody] User value)
        {
            value.Id = Guid.Empty;
            value.FirebaseId = UserFirebaseId;
            this._usersService.SignUp(value);
        }

        // PUT api/<UsersController>/5
        [HttpPut("{id}")]
        public void Put(Guid id, [FromBody] User value)
        {
        }

        // DELETE api/<UsersController>/5
        [HttpDelete("{id}")]
        public void Delete(int id)
        {
        }
    }
}
