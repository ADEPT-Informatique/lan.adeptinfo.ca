using api_adept.Models;
using api_adept.Services;
using System.Security.Claims;

namespace api_adept.Core
{
    public class AuthenticationMiddleWare
    {
        private readonly RequestDelegate _next;
        public AuthenticationMiddleWare(RequestDelegate next)
        {
            _next = next;
        }

        public async Task Invoke(HttpContext context, IUsersService authService)
        {
            //On trouve le User à partir du UserId de Firebase

            ClaimsPrincipal user = context.User;
            string id = user.Claims.FirstOrDefault(x => x.Type.ToUpper() == "USER_ID")?.Value;

            User authenticatedUser = authService.GetByFirebaseId(id);
            context.Items["User"] = authenticatedUser;

            await _next(context);
        }
    }
}
