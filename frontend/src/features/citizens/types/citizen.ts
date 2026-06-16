/** Cidadão como retornado pela API (CPF já formatado). */
export interface Citizen {
  id: string
  name: string
  cpf: string
  createdAt: string
}

/** Payload de cadastro de cidadão. */
export interface CreateCitizenPayload {
  name: string
  cpf: string
}
