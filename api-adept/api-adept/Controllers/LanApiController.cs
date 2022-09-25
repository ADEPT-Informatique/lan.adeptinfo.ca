using api_adept.Models;
using api_adept.Services;
using Microsoft.AspNetCore.Mvc;

namespace api_adept.Controllers
{
    [ApiController]
    [Route("/api/lan")]
    public class LanApiController : AdeptController
    {
        private ILanService _lanService;
        public LanApiController(IUsersService _userService, ILanService lanService) : base(_userService)
        {
            _lanService = lanService;
        }

        [HttpGet("current")]
        public Lan GetCurrentLan()
        {
            Lan currentLan = _lanService.GetLatestLan();

            return currentLan;
        }

        [HttpPost("create")]
        public Lan Create([FromBody] Lan lan)
        {
            Lan newLan = _lanService.Create(lan);

            return newLan;
        }
    }
}
