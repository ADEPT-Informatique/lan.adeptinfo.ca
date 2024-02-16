﻿// <auto-generated />
using System;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Infrastructure;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;
using Microsoft.EntityFrameworkCore.Storage.ValueConversion;
using api_adept.Context;

#nullable disable

namespace api_adept.Migrations
{
    [DbContext(typeof(AdeptLanContext))]
    [Migration("20220924025138_LansUpdate")]
    partial class LansUpdate
    {
        protected override void BuildTargetModel(ModelBuilder modelBuilder)
        {
#pragma warning disable 612, 618
            modelBuilder
                .HasAnnotation("ProductVersion", "6.0.9")
                .HasAnnotation("Relational:MaxIdentifierLength", 128);

            SqlServerModelBuilderExtensions.UseIdentityColumns(modelBuilder, 1L, 1);

            modelBuilder.Entity("api_adept.Models.Lan", b =>
                {
                    b.Property<long>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("bigint");

                    SqlServerPropertyBuilderExtensions.UseIdentityColumn(b.Property<long>("Id"), 1L, 1);

                    b.Property<DateTime>("InscriptionDate")
                        .HasColumnType("datetime2");

                    b.Property<string>("Session")
                        .HasColumnType("nvarchar(max)");

                    b.Property<DateTime>("StartingDate")
                        .HasColumnType("datetime2");

                    b.HasKey("Id");

                    b.ToTable("Lans");
                });

            modelBuilder.Entity("api_adept.Models.Participant", b =>
                {
                    b.Property<long>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("bigint");

                    SqlServerPropertyBuilderExtensions.UseIdentityColumn(b.Property<long>("Id"), 1L, 1);

                    b.Property<string>("Email")
                        .HasColumnType("nvarchar(450)");

                    b.Property<string>("FirstName")
                        .HasColumnType("nvarchar(max)");

                    b.Property<string>("LastName")
                        .HasColumnType("nvarchar(max)");

                    b.Property<long?>("ReservationId")
                        .HasColumnType("bigint");

                    b.HasKey("Id");

                    b.HasIndex("Email")
                        .IsUnique()
                        .HasFilter("[Email] IS NOT NULL");

                    b.HasIndex("ReservationId");

                    b.ToTable("Participants");
                });

            modelBuilder.Entity("api_adept.Models.Reservation", b =>
                {
                    b.Property<long>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("bigint");

                    SqlServerPropertyBuilderExtensions.UseIdentityColumn(b.Property<long>("Id"), 1L, 1);

                    b.Property<DateTime>("Date")
                        .HasColumnType("datetime2");

                    b.Property<long?>("LanId")
                        .HasColumnType("bigint");

                    b.Property<long>("SeatId")
                        .HasColumnType("bigint");

                    b.HasKey("Id");

                    b.HasIndex("LanId");

                    b.HasIndex("SeatId")
                        .IsUnique();

                    b.ToTable("Reservations");
                });

            modelBuilder.Entity("api_adept.Models.Seat", b =>
                {
                    b.Property<long>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("bigint");

                    SqlServerPropertyBuilderExtensions.UseIdentityColumn(b.Property<long>("Id"), 1L, 1);

                    b.Property<long?>("LanId")
                        .HasColumnType("bigint");

                    b.Property<int>("Number")
                        .HasColumnType("int");

                    b.Property<string>("Section")
                        .IsRequired()
                        .HasColumnType("nvarchar(1)");

                    b.Property<Guid?>("UserId")
                        .HasColumnType("uniqueidentifier");

                    b.HasKey("Id");

                    b.HasIndex("LanId");

                    b.HasIndex("UserId");

                    b.ToTable("Seats");
                });

            modelBuilder.Entity("api_adept.Models.User", b =>
                {
                    b.Property<Guid>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("uniqueidentifier");

                    b.Property<string>("DisplayName")
                        .HasColumnType("nvarchar(max)");

                    b.Property<string>("Email")
                        .HasColumnType("nvarchar(max)");

                    b.Property<string>("FirebaseId")
                        .HasColumnType("nvarchar(max)");

                    b.HasKey("Id");

                    b.ToTable("Users");
                });

            modelBuilder.Entity("api_adept.Models.Participant", b =>
                {
                    b.HasOne("api_adept.Models.Reservation", "Reservation")
                        .WithMany()
                        .HasForeignKey("ReservationId");

                    b.Navigation("Reservation");
                });

            modelBuilder.Entity("api_adept.Models.Reservation", b =>
                {
                    b.HasOne("api_adept.Models.Lan", "Lan")
                        .WithMany()
                        .HasForeignKey("LanId");

                    b.HasOne("api_adept.Models.Seat", "Seat")
                        .WithOne("Reservation")
                        .HasForeignKey("api_adept.Models.Reservation", "SeatId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();

                    b.Navigation("Lan");

                    b.Navigation("Seat");
                });

            modelBuilder.Entity("api_adept.Models.Seat", b =>
                {
                    b.HasOne("api_adept.Models.Lan", "Lan")
                        .WithMany("Seats")
                        .HasForeignKey("LanId");

                    b.HasOne("api_adept.Models.User", null)
                        .WithMany("seats")
                        .HasForeignKey("UserId");

                    b.Navigation("Lan");
                });

            modelBuilder.Entity("api_adept.Models.Lan", b =>
                {
                    b.Navigation("Seats");
                });

            modelBuilder.Entity("api_adept.Models.Seat", b =>
                {
                    b.Navigation("Reservation");
                });

            modelBuilder.Entity("api_adept.Models.User", b =>
                {
                    b.Navigation("seats");
                });
#pragma warning restore 612, 618
        }
    }
}
