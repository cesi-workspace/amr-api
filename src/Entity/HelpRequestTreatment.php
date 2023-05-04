<?php

namespace App\Entity;

use App\Repository\HelpRequestTreatmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: "help_request_treatment")]
#[ORM\Entity(repositoryClass: HelpRequestTreatmentRepository::class)]
#[ORM\Index(columns: ["help_request_id"], name: "FK_HELP_REQUEST_TREATMENT_HELP_REQUEST")]
#[ORM\Index(columns: ["helper_id"], name: "FK_HELP_REQUEST_TREATMENT_HELPER")]
#[ORM\Index(columns: ["type_id"], name: "FK_HELP_REQUEST_TREATMENT_HELP_REQUEST_TREATMENT_TYPE")]
class HelpRequestTreatment
{
    #[ORM\ManyToOne(targetEntity: HelpRequestTreatmentType::class)]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id", nullable: false)]
    private HelpRequestTreatmentType $type;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\OneToOne(targetEntity: HelpRequest::class)]
    #[ORM\JoinColumn(name: "help_request_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private HelpRequest $helpRequest;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "helper_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private User $helper;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name:"created_at", type:"datetime", nullable:false)]
    private \DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name:"updated_at", type:"datetime", nullable:true)]
    private ?\DateTime $updatedAt;

    

    public function getType(): HelpRequestTreatmentType
    {
        return $this->type;
    }

    public function setType(HelpRequestTreatmentType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getHelpRequest(): HelpRequest
    {
        return $this->helpRequest;
    }

    public function setHelpRequest(HelpRequest $helpRequest): self
    {
        $this->helpRequest = $helpRequest;

        return $this;
    }

    public function getHelper(): User
    {
        return $this->helper;
    }

    public function setHelper(User $helper): self
    {
        $this->helper = $helper;

        return $this;
    }
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
