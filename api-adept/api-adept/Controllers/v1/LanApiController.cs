using api_adept.Models;
using api_adept.Services;
using Microsoft.AspNetCore.Mvc;

namespace api_adept.Controllers.v1
{
    [ApiController]
    [Route("/api/v1/lan")]
    public class LanApiController : AdeptController
    {
        public LanApiController(IUsersService userService) : base(userService)
        {
        }

        [HttpGet]
        [Route("{session}")]
        public Lan GetLan(string session)
        {
            return new Lan(session);
        }
    }
}
