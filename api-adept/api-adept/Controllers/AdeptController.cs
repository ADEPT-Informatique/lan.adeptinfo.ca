using api_adept.Models;
using api_adept.Models.Errors;
using api_adept.Services;
using Microsoft.AspNetCore.Mvc;
using System.Net;

namespace api_adept.Controllers
{
    public abstract class AdeptController : ControllerBase
    {
        protected IUsersService _usersService;
        public new User User
        {
            get
            {
                var id = UserFirebaseId;
                if (!string.IsNullOrWhiteSpace(id))
                {
                    User user = this._usersService.GetByFirebaseId(id);
                    if (user == null)
                    {
                        throw new AdeptException("AUTH__UNREGISTERED", "This user is not registered on the platform", HttpStatusCode.NotFound);

                    }
                    else
                    {
                        return user;
                    }
                }
                return null;
            }
        }
        public string UserFirebaseId
        {
            get
            {
                return HttpContext.User.Claims.FirstOrDefault(a => a.Type == "user_id")?.Value;
            }
        }

        public AdeptController(IUsersService userService)
        {
            this._usersService = userService;
        }
    }
}
