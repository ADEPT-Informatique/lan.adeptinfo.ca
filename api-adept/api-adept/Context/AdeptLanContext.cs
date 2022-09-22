using api_adept.Models;
using Microsoft.EntityFrameworkCore;

namespace api_adept.Context
{
    public class AdeptLanContext : DbContext
    {
        public DbSet<Lan> Lans { get; set; }
        public DbSet<Seat> Seats { get; set; }
        public DbSet<Reservation> Reservations { get; set; }
        public DbSet<Participant> Participants { get; set; }

        public DbSet<User> Users { get; set; }

        public AdeptLanContext(DbContextOptions<AdeptLanContext> options) : base(options)
        {

        }
    }
}
