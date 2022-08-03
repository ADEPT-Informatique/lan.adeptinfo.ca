using api_adept.Models;
using Microsoft.AspNetCore.Mvc;

namespace api_adept.Controllers
{
    [ApiController]
    [Route("/api/v1/lan")]
    public class LanApiController : BaseApiController
    {
        [HttpGet]
        [Route("{session}")]
        public Lan GetLan(string session)
        {
            return new Lan(session);
        }
    }
}
