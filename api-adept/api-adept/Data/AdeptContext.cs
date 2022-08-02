using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.EntityFrameworkCore;
using api_adept.Models;

    public class AdeptContext : DbContext
    {
        public AdeptContext (DbContextOptions<AdeptContext> options)
            : base(options)
        {
        }

        public DbSet<api_adept.Models.Lan> Lan { get; set; } = default!;
    }
