using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using api_adept.Models;
using api_adept.Context;

namespace api_adept.Controllers
{
    [Route("api/[controller]/[action]")]
    [ApiController]
    public class LansController : ControllerBase
    {
        private readonly AdeptLanContext _context;

        public LansController(AdeptLanContext context)
        {
            _context = context;
        }

        // GET: api/Lans
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Lan>>> GetLan()
        {
          if (_context.Lans == null)
          {
              return NotFound();
          }
            return await _context.Lans.ToListAsync();
        }

        // GET: api/Lans/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Lan>> GetLan(int id)
        {
          if (_context.Lans == null)
          {
              return NotFound();
          }
            var lan = await _context.Lans.FindAsync(id);

            if (lan == null)
            {
                return NotFound();
            }

            return lan;
        }

        // PUT: api/Lans/5
        // To protect from overposting attacks, see https://go.microsoft.com/fwlink/?linkid=2123754
        [HttpPut("{id}")]
        public async Task<IActionResult> PutLan(int id, Lan lan)
        {
            if (id != lan.Id)
            {
                return BadRequest();
            }

            _context.Entry(lan).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!LanExists(id))
                {
                    return NotFound();
                }
                else
                {
                    throw;
                }
            }

            return NoContent();
        }

        // POST: api/Lans
        // To protect from overposting attacks, see https://go.microsoft.com/fwlink/?linkid=2123754
        [HttpPost]
        public async Task<ActionResult<Lan>> PostLan(Lan lan)
        {
          if (_context.Lans == null)
          {
              return Problem("Entity set 'AdeptContext.Lan'  is null.");
          }
            _context.Lans.Add(lan);
            await _context.SaveChangesAsync();

            return CreatedAtAction("GetLan", new { id = lan.Id }, lan);
        }

        // DELETE: api/Lans/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteLan(int id)
        {
            if (_context.Lans == null)
            {
                return NotFound();
            }
            var lan = await _context.Lans.FindAsync(id);
            if (lan == null)
            {
                return NotFound();
            }

            _context.Lans.Remove(lan);
            await _context.SaveChangesAsync();

            return NoContent();
        }

        private bool LanExists(int id)
        {
            return (_context.Lans?.Any(e => e.Id == id)).GetValueOrDefault();
        }
    }
}
